<?php

namespace backend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SakipTitle;

/**
 * SakipTitleSearch represents the model behind the search form of `backend\models\SakipTitle`.
 */
class SakipTitleSearch extends SakipTitle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reftitle_id'], 'integer'],
            [['nama_title'], 'safe'],
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
        $query = SakipTitle::find();

        // add conditions that should always apply here

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
            'reftitle_id' => $this->reftitle_id,
        ]);

        $query->andFilterWhere(['like', 'nama_title', $this->nama_title]);

        return $dataProvider;
    }
}
