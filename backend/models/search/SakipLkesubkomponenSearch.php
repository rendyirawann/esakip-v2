<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipLkesubkomponen;

/**
 * SakipLkesubkomponenSearch represents the model behind the search form of `backend\models\SakipLkesubkomponen`.
 */
class SakipLkesubkomponenSearch extends SakipLkesubkomponen
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reflkesubkomponen_id', 'reflkekomponen_id'], 'integer'],
            [['uraian_lkesubkomponen', 'bobot_lkesubkomponen'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SakipLkesubkomponen::find();

        // add conditions that should always apply here
        $query->with(['refLkekomponen']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'reflkesubkomponen_id' => $this->reflkesubkomponen_id,
            'reflkekomponen_id' => $this->reflkekomponen_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_lkesubkomponen', $this->uraian_lkesubkomponen])
            ->andFilterWhere(['like', 'bobot_lkesubkomponen', $this->bobot_lkesubkomponen]);

        return $dataProvider;
    }
}
