<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\SakipVisi;

/**
 * SakipVisiSearch represents the model behind the search form of `frontend\models\SakipVisi`.
 */
class SakipVisiSearch extends SakipVisi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refvisi_id', 'refperiode_5tahun_id'], 'integer'],
            [['uraian_visi', 'penjabaran_visi', 'visi_isaktif'], 'safe'],
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
        $query = SakipVisi::find();

        // Eager load relations for performance
        $query->with(['periode5Tahun']);

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
            'refvisi_id' => $this->refvisi_id,
            'refperiode_5tahun_id' => $this->refperiode_5tahun_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_visi', $this->uraian_visi])
            ->andFilterWhere(['like', 'penjabaran_visi', $this->penjabaran_visi])
            ->andFilterWhere(['like', 'visi_isaktif', $this->visi_isaktif]);

        return $dataProvider;
    }
}
