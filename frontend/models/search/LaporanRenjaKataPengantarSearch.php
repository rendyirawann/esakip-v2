<?php

namespace frontend\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\LaporanRenjaKataPengantar;

/**
 * LaporanRenjaKataPengantarSearch represents the model behind the search form of `frontend\models\LaporanRenjaKataPengantar`.
 */
class LaporanRenjaKataPengantarSearch extends LaporanRenjaKataPengantar
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['laporan_renja_kata_pengantar_id', 'refperiode_id', 'refskpd_id'], 'integer'],
            [['uraian_katapengantar', 'halaman_renja'], 'safe'],
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
        $query = LaporanRenjaKataPengantar::find();

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
            'laporan_renja_kata_pengantar_id' => $this->laporan_renja_kata_pengantar_id,
            'refperiode_id' => $this->refperiode_id,
            'refskpd_id' => $this->refskpd_id,
        ]);

        $query->andFilterWhere(['like', 'uraian_katapengantar', $this->uraian_katapengantar])
            ->andFilterWhere(['like', 'halaman_renja', $this->halaman_renja]);

        return $dataProvider;
    }
}
